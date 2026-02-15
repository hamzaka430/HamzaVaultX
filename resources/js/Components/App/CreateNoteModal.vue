<script setup>
import { nextTick, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/App/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "../PrimaryButton.vue";

const { modelValue } = defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(["update:modelValue"]);

const noteTitleInput = ref(null);
const noteContentInput = ref(null);

const form = useForm({
    name: "",
    note_content: "",
    parent_id: usePage().props.rootFolder?.id ?? null,
});

const createNote = () => {
    form.post(route("notes.store"), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
        onError: () => {
            if (form.errors.name) {
                noteTitleInput.value.focus();
            } else if (form.errors.note_content) {
                noteContentInput.value.focus();
            }
        },
    });
};

const onShow = () => {
    nextTick(() => {
        noteTitleInput.value.focus();
    });
};

const closeModal = () => {
    form.clearErrors();
    form.reset();

    emit("update:modelValue");
};
</script>

<template>
    <Modal :show="modelValue" @show="onShow" max-width="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Create New Note</h2>

            <div class="mt-6">
                <InputLabel
                    for="noteTitle"
                    value="Note Title"
                    class="sr-only"
                />
                <TextInput
                    type="text"
                    id="noteTitle"
                    class="block w-full mt-1"
                    placeholder="Note Title"
                    v-model="form.name"
                    :class="
                        form?.errors?.name
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                            : ''
                    "
                    ref="noteTitleInput"
                />
                <InputError :message="form?.errors?.name" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="noteContent"
                    value="Note Content"
                    class="sr-only"
                />
                <textarea
                    id="noteContent"
                    class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="Note Content"
                    v-model="form.note_content"
                    rows="10"
                    :class="
                        form?.errors?.note_content
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                            : ''
                    "
                    ref="noteContentInput"
                ></textarea>
                <InputError :message="form?.errors?.note_content" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="createNote"
                    >Create</PrimaryButton
                >
            </div>
        </div>
    </Modal>
</template>
